const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');

module.exports = {
    entry: {
        main: './client/index.js',
    },
    output: {
        path: path.resolve(__dirname, 'public_html/dist'),
        publicPath: 'dist/',
        filename: 'js/[name].js'
    },
    module: {
        rules: [
            {
                test: /\.(css|scss)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            publicPath: '../../dist/'
                        },
                    },
                    {
                        loader: "css-loader",
                        options: { url: true },
                    },
                    {
                        loader: 'resolve-url-loader',
                        options: {},
                    },
                    'sass-loader',
                ]
            },
            {
                test: /\.(woff(2)?|ttf|eot|svg|mp4|webm|jpg)$/,
                use: {
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]',
                    },
                }
            },
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),
    ]
};
