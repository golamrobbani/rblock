/**
 * BLOCK: Listing Grid
 */
//wordpress dependencies
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
//internal dependencies
import edit from './components/edit';
import './styles/style.scss';
import './styles/editor.scss';
// Register alignments
const validAlignments = [ 'center', 'wide', 'full' ];

// Register the block
registerBlockType( 'rblock/listing-grid', {
   title: __( 'Listing Grid', 'rblock' ),
   description: __(
       'Add listing grid',
       'rblock'
   ),
   icon: 'grid-view',
   category: 'rblock',
   keywords: [
       __( 'post', 'rblock' ),
       __( 'page', 'rblock' ),
       __( 'grid', 'rblock' ),
   ],

   edit,

   // Render via PHP
   save() {
       return null;
   },
} );
