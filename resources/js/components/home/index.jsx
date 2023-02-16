import mainImage from './img/mainImage.png';

import './style.css';

// components
import QuickReservation from '../archivedreservation/quickreservation';
import Announcement from '../announcement';

// react router
import { Link } from 'react-router-dom';


export default function Home({ onReservationClick, onAnnouncementClick }) {
    return (
        <div className='Home'>
            <div className='main-image container-mb'>
                <img src={mainImage} className='img-fluid' />
            </div>
            <QuickReservation onReservationClick={onReservationClick} />

            <Announcement onAnnouncementClick={onAnnouncementClick} />
            <p className='Announcement-more'><Link to='/announcement'>more details</Link></p>
        </div>
    );
}




