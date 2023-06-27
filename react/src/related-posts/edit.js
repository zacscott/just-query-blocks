
import {
	__
} from '@wordpress/i18n';

import {
	useState
} from '@wordpress/element';

import {
	useBlockProps,
	InnerBlocks, 
	BlockControls, 
	InspectorControls,
	AlignmentToolbar
} from '@wordpress/block-editor';

import './editor.scss';

export default function edit(props) {
	const {
        className,
        attributes: { title, mediaURL, body, alignment }
    } = props;

	return (
		<>
			<BlockControls key="controls">
				<AlignmentToolbar
					value={alignment}
					onChange={nextAlign => {
						setAttributes({ alignment: nextAlign });
					}}
				/>
			</BlockControls>
			<InspectorControls key="setting">

			</InspectorControls>
			<div { ...useBlockProps() }>
				<InnerBlocks
					templateLock={ false }
					allowedBlocks={ '*' }
				/>
			</div>
		</>
	);
}
