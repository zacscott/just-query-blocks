
import {
	__
} from '@wordpress/i18n';

import apiFetch from '@wordpress/api-fetch';

import { 
	useState,
	useEffect
} from '@wordpress/element';

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

	// Load all categories for the category select from the REST API.
	const [ categoryOptions, setCategoryOptions ] = useState( {} );
	useEffect(
		() => {
			
			const loadCategoryOptions = async () => {

				await apiFetch( { path: '/wp/v2/categories' } ).then(
					categories => {
		
						let categoryOptions = categories.map( category => {
							return {
								label: category.name,
								value: category.id,	
							};
						} );
		
						setCategoryOptions( categoryOptions );

					}
				);
			};

			loadCategoryOptions();

		},
		[]
	);

	// Load all tags for the tag select from the REST API.
	const [ tagOptions, setTagOptions ] = useState( {} );
	useEffect(
		() => {
			
			const loadTagOptions = async () => {

				await apiFetch( { path: '/wp/v2/tags' } ).then(
					tags => {
		
						let tagOptions = tags.map( tag => {
							return {
								label: tag.name,
								value: tag.id,	
							};
						} );
		
						setTagOptions( tagOptions );

					}
				);
			};

			loadTagOptions();

		},
		[]
	);

	// Load all authors for the author select from the REST API.
	const [ authorOptions, setAuthorOptions ] = useState( {} );
	useEffect(
		() => {
			
			const loadAuthorOptions = async () => {

				await apiFetch( { path: '/wp/v2/users' } ).then(
					users => {

						let authorOptions = users.map( user => {
							return {
								label: user.name,
								value: user.id,	
							};
						} );
		
						setAuthorOptions( authorOptions );

					}
				);
			};

			loadAuthorOptions();

		},
		[]
	);

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
							options={ categoryOptions }
							onChange={ value => setAttributes( { category: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ComboboxControl
							label="Tag"
							value={ tag }
							options={ tagOptions }
							onChange={ value => setAttributes( { tag: value } ) }
							__nextHasNoMarginBottom
						/>
					</PanelRow>

					<PanelRow>
						<ComboboxControl
							label="Author"
							value={ author }
							options={ authorOptions }
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
