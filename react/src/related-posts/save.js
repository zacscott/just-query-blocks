
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save(props) {

	const {
        attributes: { related_by },
		setAttributes
    } = props;

	return (
		<div { ...useBlockProps.save() }>
			<InnerBlocks.Content />
		</div>
	);

}
