import edit from './edit';

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { dateI18n, format, __experimentalGetSettings } from '@wordpress/date';
import { setState } from '@wordpress/compose';


registerBlockType( 'cdash-bd-blocks/login-form', {
    title: 'Member Login Form',
    icon: 'list-view',
    category: 'cd-blocks',
    description: __('The Member Login Form block displays the login form.', 'cdashmm'),
    example: {
    },
    
    edit: edit,
    save(){
        //Rendering in PHP
        return null;
    },
});