/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */

import { useBlockProps, InnerBlocks, BlockControls, AlignmentToolbar } from '@wordpress/block-editor';
import { QueryControls } from '@wordpress/components';

import './editor.scss';

export default function Edit(props) {
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
			<div { ...useBlockProps() }>
				<InnerBlocks
					templateLock={ false }
					allowedBlocks={ '*' }
				/>
				<QueryControls/>
			</div>
		</>
	);
}
