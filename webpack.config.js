const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');

module.exports = {
    entry: {
        main: './client/index.js',
        home: './client/js/home.js',
        new: './client/new.js',
    },
    output: {
        path: path.resolve(__dirname, 'public_html/dist'),
        filename: 'js/[name].js'
    },
    module: {
        rules: [
            {
                test: /\.(css|scss)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: { url: false }
                    },
                    'sass-loader',
                ]
            },
            {
                test: /\.(mp4|webm)$/,
                use: {
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]',
                        // outputPath: 'assets/videos/',
                        publicPath: 'dist/'
                    },
                }
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),
    ]
};
