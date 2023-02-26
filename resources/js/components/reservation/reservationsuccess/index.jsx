import './style.css';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

// react router
import { Link, useNavigate, useLoaderData } from 'react-router-dom';

import { useEffect, useRef } from 'react';

export function loader() {
    let reservationstatus = sessionStorage.getItem('reservationsuccess') || sessionStorage.getItem('reservationcancel');
    reservationstatus = JSON.parse(reservationstatus);
    console.log('reservationstatus:', reservationstatus);
    if (reservationstatus == null) {
        // console.log('inside of if statement');
        // navigate(-1);
        // console.log('after navigate function');
        throw Error();
    }

    return reservationstatus;

}


export default function ReservationSuccess({ title, description, linkLabel, link, backgroundStyle, iconStyle, isSuccess }) {
    // const navigate = useNavigate();
    const _ = useLoaderData();
    const isMounted = useRef(false);


    useEffect(() => {
        if (!isMounted.current) {
            isMounted.current = true;
        } else {
            sessionStorage.removeItem('reservationsuccess');
            sessionStorage.removeItem('reservationcancel');
        }

    }, []);

    // useEffect(() => {
    //     let reservationstatus = isSuccess ? sessionStorage.getItem('reservationsuccess') : sessionStorage.getItem('reservationcancel');
    //     reservationstatus = JSON.parse(reservationstatus);
    //     console.log('reservationstatus:', reservationstatus);
    //     if (reservationstatus == null) {
    //         console.log('inside of if statement');
    //         navigate(-1);
    //         console.log('after navigate function');
    //     }
    //     sessionStorage.removeItem('reservationstatus');
    // }, []);

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