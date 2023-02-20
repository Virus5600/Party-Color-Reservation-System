import './style.css';
import reservation_icon from './img/reservation_icon.png';
import reservaton_status from './img/reservation_status.png';

import { Link } from 'react-router-dom';

export default function ReservationSelection() {
    return (
        <>
            <div className='d-flex justify-content-center m-5'>
                <div className="row g-4 mx-5">
                    <div className="col">
                        <ReservationSelectionCard img={reservation_icon} label={'MAKE A RESERVATION'} link={'/reservation'} />
                    </div>
                    <div className="col">
                        <ReservationSelectionCard img={reservaton_status} label={'VIEW RESERVATION'} link={'/viewreservation'} />
                    </div>
                </div>
            </div>
        </>
    );
}

const ReservationSelectionCard = ({ img, label, link }) => {
    return (
        <>
            <div className='ReservationSelectionCard-outer h-100'>
                <Link to={link} className='ReservationSelectionCardInner-link'>
                    <div className='ReservationSelectionCard-inner d-flex flex-column h-100 text-center'>
                        <div>
                            <img src={img} alt='reservation icon' />
                        </div>
                        <div className='flex-grow-1 d-flex align-items-end justify-content-center'>
                            <p>
                                {label}
                            </p>
                        </div>
                    </div>
                </Link>
            </div>
        </>
    );
}