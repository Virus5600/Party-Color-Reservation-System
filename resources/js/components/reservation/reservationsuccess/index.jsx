import './style.css';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

// react router
import { Link } from 'react-router-dom';

export default function ReservationSuccess() {
    return (
        <div className='container container-small'>
            <div className='ReservationSuccess m-5 p-sm-4 p-3'>
                <span><FontAwesomeIcon icon="fa-solid fa-circle-check" className='ReeservationSuccess-icon' /></span><br />
                <span>Your reservation has been confirmed!</span><br />
                <span>An email confirmation has been sent to you.</span>
            </div>
            <div className='ReservationSuccess-links'>
                <Link to='/reservation' className='link'>make another reservation</Link><br />
                <Link to='/home' className='link'>go to Home page</Link>
            </div>
        </div>

    );
}