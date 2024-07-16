import typescript from '@rollup/plugin-typescript';
import buble from '@rollup/plugin-buble';
import terser from '@rollup/plugin-terser';

const ts = () => typescript({
	tsconfig: './tsconfig.json'
});

// use main option for each ts file entry
const optTreeUmd = (file, module) => {
	return 	{
		input: `./script/${file}.ts`,
		plugins: [
			ts(),
			buble()
		],
		output: [
			{
				file: `./dist/js/${file}.js`,
				name: module,
				format: 'umd',
				sourcemap: true
			},
			{
				file: `./dist/js/${file}.min.js`,
				name: module,
				format: 'umd',
				plugins: [terser()],
				sourcemap: true
			}
		]
	}
}

// option tree for amd files
const optTreeAmd = (file) => {
	return 	{
		input: `./script/${file}.ts`,
		plugins: [
			ts(),
			buble({
				transforms: { forOf: false }
			})
		],
		output: [
			{
				file: `./dist/js/${file}.js`,
				format: 'iife',
				sourcemap: true
			},
			{
				file: `./dist/js/${file}.min.js`,
				format: 'iife',
				plugins: [terser()],
				sourcemap: true
			}
		]
	}
}

export default [
	optTreeUmd('admin', 'WpnbAdm'),
	optTreeAmd('send-adm'),
	optTreeAmd('settings')
];