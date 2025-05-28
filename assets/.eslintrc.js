module.exports = {
    "ignorePatterns": ['*.gen.ts'],
    "env": {
        "browser": true,
        "es2021": true
    },
    "settings": {
        "react": {
            "version": "detect"
        }
    },
    "extends": [
        "standard-with-typescript",
        "plugin:react/recommended",
        "plugin:storybook/recommended"
    ],
    "parserOptions": {
        "ecmaFeatures": {
            "jsx": true
        },
    },
    "overrides": [
        {
            "env": {
                "node": true
            },
            "files": [
                ".eslintrc.{js,cjs}"
            ],
            "parserOptions": {
                "sourceType": "script"
            }
        },

        {
            "files": ["*.spec.ts", "*.spec.tsx", "*.test.ts", "*.test.tsx"],
            "env": {
              "jest": true
            }
        }
    ],
    "parserOptions": {
        "ecmaVersion": "latest",
        "sourceType": "module"
    },
    "plugins": [
        "react"
    ],
    "rules": {
        "@typescript-eslint/no-misused-promises": "off",
        "@typescript-eslint/no-non-null-assertion": "off",
        "@typescript-eslint/unbound-method": "off",
        'react/jsx-boolean-value': 'error',
        'react/jsx-closing-bracket-location': 'error',
        'react/jsx-curly-spacing': [ 'error', 'always' ],
        'react/jsx-equals-spacing': 'error',
        'react/jsx-first-prop-new-line': 'error',
        'react/jsx-indent-props': [ 'error', 2 ],
        'react/jsx-indent': [ 'error', 2 ],
        'react/jsx-key': 'error',
        'react/jsx-max-props-per-line': [ 'error', { 'maximum': 1 }],
        'react/jsx-no-literals': 'off',
        'react/jsx-no-target-blank': 'error',
        'react/jsx-pascal-case': 'error',
        'react/jsx-sort-props': 'error',
        'react/jsx-tag-spacing': 'error',
        'react/jsx-no-constructed-context-values': 'error',
        'react/jsx-wrap-multilines': [
            'error', 
            {
                "declaration": "parens-new-line",
                "assignment": "parens-new-line",
                "return": "parens-new-line",
                "arrow": "parens-new-line",
                "condition": "parens-new-line",
                "logical": "parens-new-line",
                "prop": "ignore"
            }
        ]
    }
}
