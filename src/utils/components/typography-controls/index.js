//WordPress dependencies
import { __ } from '@wordpress/i18n';
import { Component, Fragment } from '@wordpress/element';
import { compose, ifCondition } from '@wordpress/compose';
import { DOWN } from '@wordpress/keycodes';
import { RangeControl, withFallbackStyles, ToggleControl, Dropdown, ToolbarButton, SelectControl, Toolbar } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
//External dependencies
import classnames from 'classnames';
//Internal dependencies
import googleFonts from './../../components/font-family/fonts';
import TypographyAttributes from './attributes';
import TypograpyClasses from './classes';
import TypographyTransforms from './transforms';
import FontFamilyPicker from './../../components/font-family/index';
import icons from './icons';

/**
 * Export
 */
export {
	TypographyAttributes,
	TypograpyClasses,
	TypographyTransforms,
};

/**
 * Fallback styles
 */
const { getComputedStyle } = window;
const applyFallbackStyles = withFallbackStyles( ( node, ownProps ) => {
	const { textColor, fontSize, customFontSize } = ownProps.attributes;
	const editableNode = node.querySelector( '[contenteditable="true"]' );
	//verify if editableNode is available, before using getComputedStyle.
	const computedStyles = editableNode ? getComputedStyle( editableNode ) : undefined;
	return {
		fallbackTextColor: textColor || ! computedStyles ? undefined : computedStyles.color,
		fallbackFontSize: fontSize || customFontSize || ! computedStyles ? undefined : parseInt( computedStyles.fontSize ) || undefined,
	};
} );

const allowedBlocks = [ 'core/paragraph', 'core/heading', 'core/button', 'core/list','rblock/highlight' ];

/**
 * Typography Component
 */
class TypographyControls extends Component {

	render() {

		const {
			attributes,
			setAttributes,
			icon = icons.typography,
			label = __( 'Change typography', 'rblock' ),
		} = this.props;

		// Show line height on appropriate blocks.
		if ( ! allowedBlocks.includes( this.props.name ) ) {
			return null;
		}

		const {
			customFontSize,
			fontFamily,
			lineHeight,
			letterSpacing,
			noBottomSpacing,
			noTopSpacing,
			fontWeight,
			textTransform,
		} = attributes;



		const weight = [
			{
				value: '',
				label: __( 'Default', 'rblock' ),
			},
			{
				value: 'normal',
				label: __( 'Normal', 'rblock' ),
			},
			{
				value: 'bold',
				label: __( 'Bold', 'rblock' ),
			},
		];

		const transform = [
			{
				value: '',
				label: __( 'Default', 'rblock' ),
			},
			{
				value: 'uppercase',
				label: __( 'Uppercase', 'rblock' ),
			},
			{
				value: 'lowercase',
				label: __( 'Lowercase', 'rblock' ),
			},
			{
				value: 'capitalize',
				label: __( 'Capitalize', 'rblock' ),
			},
			{
				value: 'initial',
				label: __( 'Normal', 'rblock' ),
			},
		];

		if ( typeof googleFonts[ fontFamily ] !== 'undefined' && typeof googleFonts[ fontFamily ].weight !== 'undefined' ) {
			googleFonts[ fontFamily ].weight.forEach( ( k ) => {
				weight.push(
					{ value: k, label: k }
				);
			} );
		}

		const onFontChange = ( value ) => {
			setAttributes( { fontFamily: value } );

			if ( typeof googleFonts[ value ] !== 'undefined' && typeof googleFonts[ value ].weight !== 'undefined' ) {
				if ( fontWeight && Object.values( googleFonts[ fontFamily ].weight ).indexOf( fontWeight ) < 0 ) {
					setAttributes( { fontWeight: undefined } );
				}
			}
		};

		console.log("typography typography typography typography typography typography ");
		console.log(this.props.attributes);

		return (
			<Toolbar>
			<Dropdown
				className={ classnames( 'components-dropdown-menu', 'components-rblock-typography-dropdown' ) }
				contentClassName="components-dropdown-menu__popover components-rblock-typography-dropdown"
				renderToggle={ ( { isOpen, onToggle } ) => {
					const openOnArrowDown = ( event ) => {
						if ( ! isOpen && event.keyCode === DOWN ) {
							event.preventDefault();
							event.stopPropagation();
							onToggle();
						}
					};

					return (
						<ToolbarButton
							className="components-dropdown-menu__toggle"
							icon={ icon }
							onClick={ onToggle }
							onKeyDown={ openOnArrowDown }
							aria-haspopup="true"
							aria-expanded={ isOpen }
							label={ label }
							tooltip={ label }
						/>
					);
				} }

				renderContent={ () => (
					<Fragment>
						<div className="components-rblock-typography-dropdown__inner">
							
							<FontFamilyPicker
								label={ __( 'Font', 'rblock' ) }
								value={ fontFamily }
								onChange={ ( nextFontFamily ) => onFontChange( nextFontFamily ) }
								className="components-base-control--with-flex components-rblock-typography-dropdown__inner--font"
							/>

							{ ( ( typeof attributes.textPanelFontWeight === 'undefined' || ( typeof attributes.textPanelFontWeight !== 'undefined' && typeof attributes.textPanelFontWeight === 'undefined' ) ) )
								? (
									<SelectControl
										label={ __( 'Weight', 'rblock' ) }
										value={ fontWeight }
										options={ weight }
										onChange={ ( nextFontWeight ) => setAttributes( { fontWeight: nextFontWeight } ) }
										className="components-base-control--with-flex components-rblock-typography-dropdown__inner--weight"
									/>
								)
								: null
							}
							{ ( ( typeof attributes.textPanelTextTransform === 'undefined' || ( typeof attributes.textPanelTextTransform !== 'undefined' && typeof attributes.textPanelTextTransform === 'undefined' ) ) )
								? (
									<SelectControl
										label={ __( 'Transform', 'rblock' ) }
										value={ textTransform }
										options={ transform }
										onChange={ ( nextTextTransform ) => setAttributes( { textTransform: nextTextTransform } ) }
										className="components-base-control--with-flex components-rblock-typography-dropdown__inner--transform"
									/> ) : null
							}
							{ ( ( typeof attributes.textPanelHideSize === 'undefined' || ( typeof attributes.textPanelHideSize !== 'undefined' && typeof attributes.textPanelHideSize === 'undefined' ) ) )
								? (
									<RangeControl
										label={ __( 'Size', 'rblock' ) }
										value={ parseFloat( customFontSize ) || undefined }
										onChange={ ( nextFontSize ) => setAttributes( { customFontSize: nextFontSize } ) }
										min={ 1 }
										max={ 100 }
										step={ 1 }
										className="components-rblock-typography-dropdown__inner--size"
									/> ) : null
							}
							{ ( ( typeof attributes.textPanelLineHeight === 'undefined' || ( typeof attributes.textPanelLineHeight !== 'undefined' && typeof attributes.textPanelLineHeight === 'undefined' ) ) )
								? (
									<RangeControl
										label={ __( 'Line height', 'rblock' ) }
										value={ parseFloat( lineHeight ) || undefined }
										onChange={ ( nextLineHeight ) => setAttributes( { lineHeight: nextLineHeight } ) }
										min={ 1 }
										max={ 3 }
										step={ .01 }
										className="components-rblock-typography-dropdown__inner--line-height"
									/> ) : null
							}
							{ ( ( typeof attributes.textPanelLetterSpacing === 'undefined' || ( typeof attributes.textPanelLetterSpacing !== 'undefined' && typeof attributes.textPanelLetterSpacing === 'undefined' ) ) )
								? (
									<RangeControl
										label={ __( 'Letter spacing', 'rblock' ) }
										value={ parseFloat( letterSpacing ) || undefined }
										onChange={ ( nextLetterSpacing ) => setAttributes( { letterSpacing: nextLetterSpacing } ) }
										min={ -1 }
										max={ 3 }
										step={ .1 }
										className="components-rblock-typography-dropdown__inner--letter-spacing"
									/> ) : null
							}
							{ ( ( typeof attributes.textPanelShowSpacingControls !== 'undefined' && attributes.textPanelShowSpacingControls ) ) &&
								<div className="components-rblock-typography-dropdown__footer">
									<ToggleControl
										label={ __( 'No top spacing', 'rblock' ) }
										checked={ !! noTopSpacing }
										onChange={ () => setAttributes( { noTopSpacing: ! noTopSpacing } ) }
									/>
									<ToggleControl
										label={ __( 'No bottom spacing', 'rblock' ) }
										checked={ !! noBottomSpacing }
										onChange={ () => setAttributes( { noBottomSpacing: ! noBottomSpacing } ) }
									/>
								</div>
							}
						</div>
					</Fragment>
				) }
			/>
		</Toolbar>
		);
	}
}

export default compose( [
	applyFallbackStyles,
] )( TypographyControls );
