#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const process = require('process');

/**
 * Recursively process the passed directories.
 *
 * @param array dirs An array of directories.
 */
function process_dirs(dirs) {
	dirs.forEach(dir => {

		check_readme(dir);

		const results = fs.readdirSync(dir);
		check_for_same_name(dir, results.filter(res => fs.lstatSync(path.resolve(dir, res)).isFile()));
		const folders = results.filter(res => fs.lstatSync(path.resolve(dir, res)).isDirectory()).filter(res => res !== 'assets');
		const innerFolders = folders.map(folder => path.resolve(dir, folder));
		if (innerFolders.length === 0) {
			return;
		}
		process_dirs(innerFolders);
	})
}

/**
 * Check for the file README.md in the passed directory.
 *
 * @param string dir The directory to check.
 */
function check_readme(dir) {
	if (!fs.existsSync(path.resolve(dir, 'README.md'))) {
		errorList.push('Folder ' + dir + ' does not contain a README.md file');
	}
}

/**
 * Check a file with the same name as the passed directory does NOT exist.
 *
 * @param string dir The directory to check.
 */
function check_for_same_name(dir, files) {
	const dirBasename = dir.substring(dir.lastIndexOf('/') + 1 ).toLowerCase();
	files.forEach(file => {
		if (file.endsWith('.md')) {
			fileBasename = file.substring(0, file.lastIndexOf('.')).toLowerCase();
			if (fileBasename === dirBasename) {
				errorList.push('Folder ' + dir + ' contains a file with the same name: ' + file);
			}
		}
	});
}

/**
 * @var array doc_dirs Directories to check.
 */
const doc_dirs = ['docs', 'user-docs', 'other-docs'];
/**
 * @var array errorList A list of accumulated errors.
 */
const errorList = [];

process_dirs(doc_dirs);

if (errorList.length) {
	console.log('Tests failed');
	console.log(errorList);
	process.exit(1)
}

