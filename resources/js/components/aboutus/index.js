import React from 'react';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClock, faPhone, faLocationArrow } from '@fortawesome/free-solid-svg-icons';

import locationImage from './img/location.png';
import appearanceImage from './img/appearance.png';

const AboutUs = () => {
    return (
        <div className='AboutUs' id='AboutUs'>
            <h1>ABOUT US</h1>
            <div className='d-flex justify-content-evenly'>
                <TimeLocation />
                <Appearance />
            </div>

        </div>
    );
};

const Appearance = () => {
    return (
        <div className='Appearance'>
            <h2>APPEARANCE</h2>
            <div className='Appearance-image'>
                <img className='img-fluid' src={appearanceImage} alt='appearance' />
            </div>

        </div>
    );
};

const TimeLocation = () => {
    return (
        <div className='time-location'>
            <div className='row align-items-center'>
                <div className='col-auto'>
                    <FontAwesomeIcon icon={faClock} className='h5 my-0' />
                </div>
                <div className='col'>
                    17:00 - 22:00<br />
                    <i>CLOSED Monday/Tuesday</i>
                </div>
            </div>
            <hr />
            <div className='row align-items-center'>
                <div className='col-auto'>
                    <FontAwesomeIcon icon={faPhone} className='h5 my-0' />
                </div>
                <div className='col'>
                    080-3980-4560
                </div>
            </div>
            <hr />
            <div className='row align-items-center'>
                <div className='col-auto'>
                    <FontAwesomeIcon icon={faLocationArrow} className='h5 my-0' />
                </div>
                <div>
                    <img className='img-fluid' src={locationImage} alt='location map of party color' />
                </div>
            </div>
        </div>
    );
};

export default AboutUs;