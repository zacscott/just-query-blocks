
import { registerBlockType } from '@wordpress/blocks';

import './style.scss';

import edit from './edit';
import save from './save';
import block from './block.json';

registerBlockType(
	block.name,
	{
		edit,
		save,
	}
);
