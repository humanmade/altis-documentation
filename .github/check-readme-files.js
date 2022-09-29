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
		const folders = results.filter(res => fs.lstatSync(path.resolve(dir, res)).isDirectory()).filter(res => res!== 'assets');
		const innerFolders = folders.map(folder => path.resolve(dir, folder));
		if (innerFolders.length === 0) {
			return;
		}
		//innerFolders.forEach(innerFolder => folderList.push(innerFolder));
		process_dirs(innerFolders);
	})
}
/**
 * Check for the file README.md in the passed directory.
 *
 * @param string dir The directory to check.
 */
function check_readme(dir) {
	// console.log('looking for ' + path.resolve(dir, 'README.md'));
	if( ! fs.existsSync(path.resolve(dir, 'README.md') ) )  {
		console.log( 'Folder ' + dir + ' does not contain a README.md file');
		errorList.push(dir);
	}
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

if ( errorList.length ) {
	console.log('Test failed');
	process.exit(1)
}
