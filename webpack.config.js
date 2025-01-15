const Encore = require('@symfony/webpack-encore');
const path = require('path');
const webpack = require('webpack')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath(path.resolve(__dirname, 'src', 'CoreShop', 'Bundle', 'CoreBundle', 'Resources', 'public', 'build'))
    .setPublicPath('/bundles/coreshopcore/build')

    .addEntry('main', path.resolve(__dirname, 'assets', 'js', 'src', 'main.ts'))
    .splitEntryChunks()
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableTypeScriptLoader()
    .enableReactPreset()

    // .addRule({
    //     test: /\inline\.svg$/i,
    //     use: [{
    //         loader: '@svgr/webpack',
    //         options: {
    //             icon: true,
    //             typescript: true
    //         }
    //     }],
    // })

    // Important! Reference this vendor-manifest in your build.
    // It will take care of injecting Ant-Design, React, etc. without the need to bundle it in your plugin.
    .addPlugin(new webpack.DllReferencePlugin({
        context: __dirname,
        manifest: path.join(__dirname, 'node_modules', 'pimcore-studio-ui', 'dist', 'vendor',  'vendor-manifest.json')
    }))
;

if (!Encore.isDevServer()) {
    // only needed for CDN's or sub-directory deploy
    Encore
        .setManifestKeyPrefix('bundles/coreshopcore/build')
    ;
}

let config = Encore.getWebpackConfig();

// Exclude inline SVGs for package "@svgr/webpack" from the default encore rule
config.module.rules.forEach(rule => {
    if (rule.test.toString().includes('|svg|')) {
        rule.exclude = /\.inline\.svg$/
    }
})

module.exports = config;