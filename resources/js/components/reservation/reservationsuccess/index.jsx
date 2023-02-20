import './style.css';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

// react router
import { Link, useNavigate } from 'react-router-dom';

import { useEffect } from 'react';

export default function ReservationSuccess({ title, description, linkLabel, link, backgroundStyle, iconStyle, isSuccess }) {
    const navigate = useNavigate();

    useEffect(() => {
        let reservationstatus = isSuccess ? sessionStorage.getItem('reservationsuccess') : sessionStorage.getItem('reservationcancel');
        reservationstatus = JSON.parse(reservationstatus);
        console.log(reservationstatus);
        if (reservationstatus == null) {
            navigate('/', { replace: true });
        }
        sessionStorage.removeItem('reservationstatus');
    }, []);

    return (
        <div className='container container-small'>
            <div className='ReservationSuccess m-5 p-sm-4 p-3' style={backgroundStyle}>
                <span><FontAwesomeIcon icon="fa-solid fa-circle-check" className='ReeservationSuccess-icon' style={iconStyle} /></span><br />
                <span>{title}</span><br />
                <span>{description}</span>
            </div>
            <div className='ReservationSuccess-links'>
                <Link to={link} className='link'>{linkLabel} another reservation</Link><br />
                <Link to='/' className='link'>go to Home page</Link>
            </div>
        </div>

    );
}