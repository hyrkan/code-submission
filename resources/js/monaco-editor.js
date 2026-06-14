import * as monaco from 'monaco-editor';
import editorWorker from 'monaco-editor/esm/vs/editor/editor.worker?worker';

self.MonacoEnvironment = {
    getWorker() {
        return new editorWorker();
    }
};

// Make monaco available globally
window.monaco = monaco;

// Monaco Editor Manager
window.MonacoEditorManager = {
    editors: {},

    /**
     * Create a Monaco Editor instance inside a container
     * @param {string} containerId - The DOM element ID for the editor container
     * @param {string} language - The programming language (default: 'python')
     * @param {string} value - Initial code value
     * @param {object} options - Additional Monaco editor options
     * @returns {object} The Monaco editor instance
     */
    create(containerId, language = 'python', value = '', options = {}) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container #${containerId} not found`);
            return null;
        }

        // Dispose existing editor if any
        if (this.editors[containerId]) {
            this.editors[containerId].dispose();
        }

        const defaultOptions = {
            value: value,
            language: language,
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: { enabled: false },
            scrollBeyondLastLine: false,
            fontSize: 14,
            lineNumbers: 'on',
            renderLineHighlight: 'all',
            tabSize: 4,
            insertSpaces: true,
            wordWrap: 'on',
            folding: true,
            bracketPairColorization: { enabled: true },
            autoIndent: 'full',
            formatOnPaste: true,
            formatOnType: true,
            suggestOnTriggerCharacters: true,
            acceptSuggestionOnEnter: 'smart',
            tabCompletion: 'on',
            wordBasedSuggestions: 'allDocuments',
            scrollbar: {
                verticalScrollbarSize: 10,
                horizontalScrollbarSize: 10,
            },
        };

        const editor = monaco.editor.create(container, {
            ...defaultOptions,
            ...options,
        });

        this.editors[containerId] = editor;

        // Add keyboard shortcut: Ctrl+S / Cmd+S to prevent default and trigger save
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => {
            // Dispatch a custom event that the page can listen to
            container.dispatchEvent(new CustomEvent('monaco-save', { detail: { editor } }));
        });

        return editor;
    },

    /**
     * Get the value of an editor
     * @param {string} containerId
     * @returns {string}
     */
    getValue(containerId) {
        const editor = this.editors[containerId];
        return editor ? editor.getValue() : '';
    },

    /**
     * Set the value of an editor
     * @param {string} containerId
     * @param {string} value
     */
    setValue(containerId, value) {
        const editor = this.editors[containerId];
        if (editor) {
            editor.setValue(value);
        }
    },

    /**
     * Change the language of an editor
     * @param {string} containerId
     * @param {string} language
     */
    setLanguage(containerId, language) {
        const editor = this.editors[containerId];
        if (editor) {
            const model = editor.getModel();
            if (model) {
                monaco.editor.setModelLanguage(model, language);
            }
        }
    },

    /**
     * Dispose of an editor
     * @param {string} containerId
     */
    dispose(containerId) {
        const editor = this.editors[containerId];
        if (editor) {
            editor.dispose();
            delete this.editors[containerId];
        }
    },

    /**
     * Dispose all editors
     */
    disposeAll() {
        Object.keys(this.editors).forEach((id) => {
            this.editors[id].dispose();
        });
        this.editors = {};
    },
};

// Auto-dispose on page unload
window.addEventListener('beforeunload', () => {
    window.MonacoEditorManager.disposeAll();
});