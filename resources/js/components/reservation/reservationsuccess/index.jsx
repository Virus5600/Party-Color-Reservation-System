// react
import React from 'react';


// css style
import './style.css';


// FontAwesome Icon
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';


// react router
import { Link } from 'react-router-dom';


/**
 * props
 * - title
 * - description
 * - link_label
 * - link
 * - bg_style
 * - icon_style
 */
export default function ReservationStatus(props) {

    return (
        <div className='container container-small'>
            <div className='ReservationSuccess m-5 p-sm-4 p-3' style={props.bg_style}>
                <span><FontAwesomeIcon icon="fa-solid fa-circle-check" className='ReeservationSuccess-icon' style={props.icon_style} /></span><br />
                <span>{props.title}</span><br />
                <span>{props.description}</span>
            </div>
            <div className='ReservationSuccess-links'>
                <Link to={props.link} className='link'>{props.link_label}</Link><br />
                <Link to='/' className='link'>go to Home page</Link>
            </div>
        </div>

    );
}