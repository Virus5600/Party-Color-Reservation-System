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
            <div className=''>
                <Link to="/">
                    <img src={mainImage} className='img-fluid' />
                </Link>
            </div>
            <QuickReservation />

            <Announcement />
            <p className=''><Link to='/announcements' className='moredetails'>more details</Link></p>
        </div>
    );
}




