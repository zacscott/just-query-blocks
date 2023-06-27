
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
        attributes: { relatedBy, orderBy, order, ignoreStickyPosts },
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
						<SelectControl
							label="Order By"
							value={ orderBy }
							options={ [
								{ label: 'Published Date', value: 'post_date' },
								{ label: 'Modified Date', value: 'post_modified' },
								{ label: 'Comment Count', value: 'comment_count' },
							] }
							onChange={ value => setAttributes( { orderBy: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<SelectControl
							label="Order"
							value={ order }
							options={ [
								{ label: 'Descending', value: 'DESC' },
								{ label: 'Ascending', value: 'ASC' },
								{ label: 'Random', value: 'RAND' },
							] }
							onChange={ value => setAttributes( { order: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ToggleControl
							label="Ignore sticky posts"
							checked={ ignoreStickyPosts }
							onChange={ value => { setAttributes( { ignoreStickyPosts: value } ) } }
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
