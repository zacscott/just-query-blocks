
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
	ComboboxControl,
	ToggleControl,
} from '@wordpress/components';

import './editor.scss';

export default function edit( props ) {
	const {
        attributes: { category, tag, author, orderBy, order, ignoreStickyPosts },
		setAttributes
    } = props;

	return (
		<>

			<InspectorControls>

				<PanelBody
					title={ __( 'Query' ) }
					initialOpen={true}
				>

					<PanelRow>
						<ComboboxControl
							label="Category"
							value={ category }
							options={ [
								{ label: 'Category', value: '123' },
							] }
							onChange={ value => setAttributes( { category: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ComboboxControl
							label="Tag"
							value={ tag }
							options={ [
								{ label: 'Tag', value: '123' },
							] }
							onChange={ value => setAttributes( { tag: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ComboboxControl
							label="Author"
							value={ author }
							options={ [
								{ label: 'Author', value: '123' },
							] }
							onChange={ value => setAttributes( { author: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ComboboxControl
							label="Order By"
							value={ orderBy }
							options={ [
								{ label: 'Published Date', value: 'date' },
								{ label: 'Modified Date', value: 'modified' },
								{ label: 'Comment Count', value: 'comment_count' },
								{ label: 'Random', value: 'rand' },
							] }
							onChange={ value => setAttributes( { orderBy: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ComboboxControl
							label="Order"
							value={ order }
							options={ [
								{ label: 'Descending', value: 'DESC' },
								{ label: 'Ascending', value: 'ASC' },
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
