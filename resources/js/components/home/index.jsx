import mainImage from './img/mainImage.png';

import './style.css';

// components
import QuickReservation from '../archivedreservation/quickreservation';
import Announcement from '../announcement';

// react router
import { Link } from 'react-router-dom';


export default function Home() {
    return (
        <div className='Home'>
            <div className='d-flex justify-content-center'>
                <div>

                    <img src={mainImage} className='img-fluid' />

                </div>
            </div>

            <QuickReservation />

            <Announcement />
            <p className='text-center bg-white'><Link to='/announcements' className='moredetails'>See More</Link></p>
        </div>
    );
}




