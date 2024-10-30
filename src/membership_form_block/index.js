import edit from './edit';

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { dateI18n, format, __experimentalGetSettings } from '@wordpress/date';
import { setState } from '@wordpress/compose';


registerBlockType( 'cdash-bd-blocks/membership-form', {
    title: 'Membership Form',
    icon: 'list-view',
    category: 'cd-blocks',
    description: __('The Membership Form block displays the membership form to add new members.', 'cdashmm'),
    example: {
    },
    attributes: {
        action:{
            type: 'string',
            default: 'signup',
        },
        customFieldsArray:{
            type: 'array',
            default: []
        },
        busDetailsSectionTitle:{
            type: 'string',
            default: __('Business Details', 'cdashmm'),
        },
        customFieldsSectionTitle:{
            type: 'string',
            default: __('Custom Fields', 'cdashmm'),
        },
        addDescriptionField:{
            type: 'boolean',
            default: false,
        },
        addLogoUpload:{
            type: 'boolean',
            default: false,
        }
    },
    edit: edit,
    save(){
        //Rendering in PHP
        return null;
    },
});