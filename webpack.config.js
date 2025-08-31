const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');

const copyPlugin = defaultConfig.plugins.find(
	(plugin) => plugin instanceof CopyWebpackPlugin
);
copyPlugin.patterns.push({
	from: path.resolve(__dirname, 'src', 'assets', 'images'),
	to: path.resolve(__dirname, 'build', 'assets', 'images'),
});

module.exports = (env) => {
	return {
		...defaultConfig,
		entry: {
			bundle: path.resolve(__dirname, 'src', 'index.js'),
			admin: path.resolve(__dirname, 'src', 'admin', 'index.js'),
		},
		output: {
			filename: 'js/[name].js',
			path: path.resolve(__dirname, 'build'),
			clean: true,
		},
		externals: {
			ymaps3: 'ymaps3',
		},
		plugins: [
			...defaultConfig.plugins.filter(
				(plugin) =>
					!(plugin instanceof MiniCssExtractPlugin) &&
					!(plugin instanceof DependencyExtractionWebpackPlugin)
			),
			new MiniCssExtractPlugin({
				filename: 'css/[name].css',
			}),
			new DependencyExtractionWebpackPlugin({
				outputFilename: './bundle.asset.php',
			}),
		],
		module: {
			rules: [...defaultConfig.module.rules],
		},
	};
};
