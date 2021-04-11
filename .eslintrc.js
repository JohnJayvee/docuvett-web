module.exports = {
    parser: 'vue-eslint-parser',
    extends: [
        'plugin:vue/recommended'
    ],
    parserOptions: {
        'parser': 'babel-eslint',
        'ecmaVersion': 2020,
        'sourceType': 'module'
    },
    plugins: [
        'import',
        'vue'
    ],
    rules: {
        'vue/html-indent': ['error', 4, {
            'attribute': 1,
            'baseIndent': 1,
            'closeBracket': 0,
            'alignAttributesVertically': true
        }],
        'vue/script-indent': ['error', 4, {
            'baseIndent': 1,
            'switchCase': 1,
            'ignores': []
        }],
        'semi': [
            'error',
            'always'
        ],
        'vue/max-attributes-per-line': ['error', {
            'singleline': 10,
            'multiline': {
                'max': 1,
                'allowFirstLine': false
            }
        }],
        'vue/no-spaces-around-equal-signs-in-attribute': [
            'error'
        ],
        'vue/no-multi-spaces': ['error', {
            'ignoreProperties': false
        }],
        'vue/html-quotes': ['error', 'double', {
            'avoidEscape': false
        }],
        'vue/html-closing-bracket-newline': ['error', {
            'singleline': 'never',
            'multiline': 'never'
        }],
        'vue/html-self-closing': ['error', {
            'html': {
                'normal': 'never',
                'void': 'never',
                'component': 'never'
            },
            'svg': 'always',
            'math': 'always'
        }],
        'space-before-function-paren': 'off'
    },
    overrides: [
        {
            'files': ['*.js', '*.jsx'],
            'rules': {
                'indent': ['error', 4],
            }
        }
    ]
};