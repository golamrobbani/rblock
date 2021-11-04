/**
 * External dependencies
 */
//wordpress dependencies
const { __ } = wp.i18n;
const { compose } = wp.compose;
const { Component, Fragment } = wp.element;
const { decodeEntities } = wp.htmlEntities;
const { withSelect } = wp.data;
const { Placeholder, Spinner, Toolbar } = wp.components;
const { BlockAlignmentToolbar, BlockControls } = wp.blockEditor;

//external dependencies
import isUndefined from 'lodash/isUndefined';
import pickBy from 'lodash/pickBy';
import moment from 'moment';
import classnames from 'classnames';

//internal dependencies
import Inspector from './inspector';


export default class EditListingGrid extends Component {
   render() {
       const { attributes, setAttributes, latestPosts } = this.props;

       return (
           <Fragment>
               <Inspector { ...{ setAttributes, ...this.props } } />

               
                <h1>Listing Grid Edit</h1>
           </Fragment>
       );
   }
}


