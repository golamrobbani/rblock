/**
 * Inspector Controls
 */
//wordpress dependencies
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const {
	PanelBody,
	QueryControls,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} = wp.components;
const { addQueryArgs } = wp.url;
const { apiFetch } = wp;
//internal dependencies
import RenderSettingControl from '../../../utils/components/settings/renderSettingControl';
//external dependencies
import axios from 'axios';
import Qs from 'qs';

/**
 * Create an Inspector Controls wrapper Component
 */
export default class Inspector extends Component {
	constructor() {
		super( ...arguments );
		this.state = { 
			categoriesList: [],
		};
	}

	componentDidMount() {
		this.stillMounted = true;
		let data = {
			action: 'rtcl_categories',
			rtcl_nonce: rblock_script.rtcl_nonce
		}

		axios.post(rblock_script.ajaxurl, Qs.stringify( data ))
			.then(( response ) => {
				if ( this.stillMounted ) {
					let catListings=[];
					let ai=0;
					for (const [value, label] of Object.entries(response.data)) {
						catListings[ai]={value,label};
						ai++;
					}
					this.setState( { categoriesList: catListings } );
				}
			})
			.catch((error) => {
				if ( this.stillMounted ) {
					this.setState( { 
						categoriesList: [] 
					} );
				}
			});

	}

	componentWillUnmount() {
		this.stillMounted = false;
	}



	render() {
		// Setup the attributes
		const { attributes, setAttributes,latestPosts } = this.props;
		const { order, orderBy } = attributes;
		const { categoriesList } = this.state;
		

		// style type options
		const styleOptions = [
			{ value: '1', label: __( 'Style 1', 'rblock' ) },
			{ value: '2', label: __( 'Style 2', 'rblock' ) },
			{ value: '3', label: __( 'Style 3', 'rblock' ) },
			{ value: '4', label: __( 'Style 4', 'rblock' ) },
			{ value: '5', label: __( 'Style 5', 'rblock' ) },
			{ value: '6', label: __( 'Style 6', 'rblock' ) },
		];
		//item to show
		const itemShowOptiosn = [
			{ value: 'all', label: __( 'All', 'rblock' ) },
			{ value: 'featured', label: __( 'Featured', 'rblock' ) },
			{ value: 'new'     , label: __( 'New', 'rblock' ) },
			{ value: 'popular' , label: __( 'Popular', 'rblock' ) },
			{ value: 'top'     , label: __( 'Top', 'rblock' ) },
			{ value: 'custom'  , label: __( 'Custom', 'rblock' ) },
		];

		


		return (
			<InspectorControls>

				<PanelBody title={ __('Listing Grid Settings','rblock') } className={'listing-grid-general-setting' }>

					<RenderSettingControl id="rb_listing_grid">
						<SelectControl
							label={ __( 'Style', 'rblock' ) }
							options={ styleOptions }
							value={ attributes.style }
							onChange={ ( value ) =>
								this.props.setAttributes( { style: value } )
							}
						/>

						<SelectControl
							label={ __( 'Items To show', 'rblock' ) }
							options={ itemShowOptiosn }
							value={ attributes.type }
							onChange={ ( value ) =>
								this.props.setAttributes( { type: value } )
							}
						/>

					</RenderSettingControl>

					
					<RenderSettingControl id="rb_listing_grid_category">

						<SelectControl
							label={ __( 'Categories', 'rblock' ) }
							options={ categoriesList }
							value={ attributes.cat }
							onChange={ ( value ) =>
								this.props.setAttributes( { cat: value } )
							}
						/>


						{/* <QueryControls
							{ ...{ order, orderBy } }
							categoriesList={ categoriesList }
							selectedCategoryId={ attributes.categories }
							onOrderChange={ ( value ) => setAttributes({ order: value }) }
							onCategoryChange={ ( value ) => setAttributes({ categories: '' !== value ? value : undefined }) }
						/> */}
					</RenderSettingControl>

					<RenderSettingControl id="rb_listing_grid_cat_display">
						<ToggleControl
							label={ __(
								'Category Name Display',
								'rblock'
							) }
							checked={ attributes.cat_display }
							onChange={ () =>
								this.props.setAttributes( {
									cat_display: ! attributes.cat_display,
								} )
							}
						/>
					</RenderSettingControl>

					<RenderSettingControl id="rb_listing_grid_field_display">
						<ToggleControl
							label={ __(
								'Show Custom Fields',
								'rblock'
							) }
							checked={ attributes.field_display }
							onChange={ () =>
								this.props.setAttributes( {
									field_display: ! attributes.field_display,
								} )
							}
						/>
					</RenderSettingControl>

					<RangeControl
						label={ __( 'Number of items', 'rblock' ) }
						value={ attributes.number }
						onChange={ ( value ) => setAttributes({ number: value }) }
						min={ 1 }
						max={ 100 }
					/>

					<ToggleControl
						label={ __(
							'Change items on every page load',
							'rblock'
						) }
						checked={ attributes.random }
						onChange={ () =>
							this.props.setAttributes( {
								random: ! attributes.random,
							} )
						}
					/>




				</PanelBody>

				<PanelBody
					title={ __(	'Listing Grid Content','rblock') }
					initialOpen={ false }
				>
					<RenderSettingControl id="rb_listing_grid_displaySectionTitle">
						<ToggleControl
							label={ __(
								'Display Section Title',
								'rblock'
							) }
							checked={ attributes.displaySectionTitle }
							onChange={ () =>
								this.props.setAttributes( {
									displaySectionTitle: ! attributes.displaySectionTitle,
								} )
							}
						/>
					</RenderSettingControl>
					{ attributes.displaySectionTitle && (
						<RenderSettingControl id="rb_listing_grid_sectionTitle">
							<TextControl
								label={ __( 'Section Title', 'rblock' ) }
								type="text"
								value={ attributes.sectionTitle }
								onChange={ ( value ) =>
									this.props.setAttributes( {
										sectionTitle: value,
									} )
								}
							/>
						</RenderSettingControl>
					) }
					

				</PanelBody>


			</InspectorControls>
		);
	}
}
