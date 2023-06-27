
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
	SelectControl
} from '@wordpress/components';

import './editor.scss';

export default function edit( props ) {
	const {
        attributes: { related_by },
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
							value={ related_by }
							options={ [
								{ label: 'Category', value: 'category' },
								{ label: 'Tag', value: 'tag' },
								{ label: 'Author', value: 'author' },
							] }
							onChange={ value => setAttributes( { related_by: value } ) }
							__nextHasNoMarginBottom
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
