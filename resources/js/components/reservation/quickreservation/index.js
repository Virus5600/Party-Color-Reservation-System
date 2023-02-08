import React from 'react';

import './style.css';


const QuickReservation = ({ onReservationClick }) => {
    return (
        <div className='quick-reservation container' id='Reservation'>
            <div Name="container">
                <div className="row">
                    <div className="Reservation-temp col-lg-6">
                        <span className='title'>BBQ</span><br />
                        <span className='caption'>(including drink all you can)</span><br />
                        <span className='time'>2hrs</span><br />
                        <button className='reserve-button' onClick={onReservationClick}>RESERVE</button>
                    </div>

                    <div className="Prices col-lg-5">
                        <div className='price-description'>
                            <span className='person-type'>Adult・senior high</span><br />
                            <span className='price'>¥3,500</span>
                        </div>

                        <div className='price-description diff-style'>
                            <span className='person-type'>junior high</span><br />
                            <span className='price'>¥2,000</span>
                        </div>

                        <div className='price-description'>
                            <span className='person-type'>elementary</span><br />
                            <span className='price'>¥1,000</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    );
};

export default QuickReservation;