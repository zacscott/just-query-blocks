
import {
	__
} from '@wordpress/i18n';

import {
	useBlockProps,
	InnerBlocks, 
	InspectorControls,
} from '@wordpress/block-editor';

import {
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';

import './editor.scss';

export default function edit( props ) {
	const {
        attributes: { relatedBy, includeStickyPosts },
		setAttributes
    } = props;

	return (
		<>

			<InspectorControls>

				<PanelBody
					title={ __( 'Related By' ) }
					initialOpen={true}
				>
					<PanelRow>
						<SelectControl
							label="Related By"
							value={ relatedBy }
							options={ [
								{ label: 'Category', value: 'category' },
								{ label: 'Tag', value: 'tag' },
								{ label: 'Author', value: 'author' },
							] }
							onChange={ value => setAttributes( { relatedBy: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label="Exclude Sticky Posts"
							help={
								includeStickyPosts
									? 'Sticky posts excluded.'
									: 'Sticky posts included.'
							}
							checked={ includeStickyPosts }
							onChange={ value => { setAttributes( { includeStickyPosts: value } ) } }
						/>
					</PanelRow>

				</PanelBody>

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
