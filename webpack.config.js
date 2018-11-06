const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');

module.exports = {
    entry: {
        main: './index.js',
        home: './resources/js/home.js'
    },
    output: {
        path: path.resolve(__dirname, 'public_html/dist'),
        filename: 'js/[name].js'
    },
    module: {
        rules: [
            {
                test: /\.(css|scss)$/,
                // use:  [  'style-loader', MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader']
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: "css-loader",
                        options: { url: false }
                    },
                    'sass-loader',
                ]
            },
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),
    ]
};
