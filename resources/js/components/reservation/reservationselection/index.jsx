import './style.css';
import reservation_icon from './img/reservation_icon.png';
import reservaton_status from './img/reservation_status.png';

import { Link } from 'react-router-dom';

export default function ReservationSelection() {
    return (
        <>
            <div className='ReservationSelection'>
                <ReservationSelectionCard img={reservation_icon} label={'MAKE A RESERVATION'} link={'/reservation'} />
                <ReservationSelectionCard img={reservaton_status} label={'VIEW RESERVATION'} link={'/viewreservation'} />
            </div>
        </>
    );
}

const ReservationSelectionCard = ({ img, label, link }) => {
    return (
        <>
            <div className='ReservationSelectionCard-outer'>
                <Link to={link} className='ReservationSelectionCardInner-link'>
                    <div className='ReservationSelectionCard-inner'>
                        <div style={{ textAlign: 'center' }}>
                            <img src={img} alt='reservation icon' />
                        </div>

                        <p>
                            {label}
                        </p>
                    </div>
                </Link>

            </div>
        </>
    );
}