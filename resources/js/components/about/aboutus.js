import React from 'react';

// for icon
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClock, faPhone, faLocationArrow } from '@fortawesome/free-solid-svg-icons';
import { faInstagram } from '@fortawesome/free-brands-svg-icons'
// import { solid, regular, brands, icon } from '@fortawesome/fontawesome-svg-core/import.macro'

// images
import location from './img/location.png';
import appearance from './img/appearance.png';

// for styling
import './aboutus.css';


const AboutUs = () =>
  <div className='AboutUs'>
    <div className='AboutUs-title'>
      <h2>ABOUT US</h2>
    </div>
    <div className='AboutUs-description d-flex justify-content-between'>
      <div className='d-flex'>
        <FontAwesomeIcon icon={faClock} className='clock-icon pt-1 px-1' />
        <p>17:00 - 22:00</p>
      </div>
      <div>
        <p>CLOSED MONDAY/TUESDAY</p>
      </div>
    </div>
    <div className='AboutUs-description d-flex'>
      <FontAwesomeIcon icon={faPhone} className='phone-icon pt-1 px-1' />
      <p>080-3980-4560</p>
    </div>
    <div className='AboutUs-description'>
      <div className='d-flex'>
        <FontAwesomeIcon icon={faLocationArrow} className='arrow-icon pt-1 px-1' />
        <p>3F, 1 Chome-2-12 Tsuboya, Naha, Okinawa 902-0065, Japan</p>
      </div>
      <div className='container-fluid' >
        <img src={location} alt='location map of party color' />
      </div>
    </div>
    <div className='AboutUs-title'>
      <h2>APPEARANCE</h2>
    </div>
    <div className='container-fluid'>
      <img src={appearance} alt='image inside of party color' />
    </div>
    <div className='AboutUs-title d-flex justify-content-between'>
      <h2>SOCIAL MEDIA</h2>
      <FontAwesomeIcon icon={faInstagram} className='pt-1 insta-icon'/>
    </div>
  </div>

export default AboutUs;