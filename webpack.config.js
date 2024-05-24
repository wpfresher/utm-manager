const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = [
    {
        ...defaultConfig,
        entry: {
            ...defaultConfig.entry(),
            // 'css/utmm-admin': './assets/src/css/admin.scss',
            // 'js/utmm-admin': './assets/src/js/admin.js',
        },
        output: {
            ...defaultConfig.output,
            filename: '[name].js',
            path: __dirname + '/assets/dist/',
        },
        plugins: [
            ...defaultConfig.plugins,
            new RemoveEmptyScriptsPlugin({
                stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
                remove: /\.(js)$/,
            }),
        ],
    },
];
