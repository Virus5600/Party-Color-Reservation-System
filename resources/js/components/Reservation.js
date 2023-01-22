import { useState } from 'react';

import axios from 'axios';

// css
import './Reservation.css';

// images
import logo from './img/logo.png';

// page
import App from './App';

const Reservation = () => {
 
  const [isReturnClicked, setReturnClicked] = useState(false);

  const handleClickReturn = () => {
    setReturnClicked(true);
  };

  if (isReturnClicked) {
    return <App />
  } else {
    return (
        <div className='Reservation'>
            <div className='logo'>
                <img src={logo} alt='logo' />
            </div>

            <ReservationForm onClickReturn={handleClickReturn} />
        
        </div>
    );
  }
};

// temporary data
const times = [
  { id: 1, time: '19:00' },
  { id: 2, time: '20:00' },
  { id: 3, time: '21:00' },
  { id: 1, time: '19:00' },

];

const API_FOR_GETTING_AVAILABLE_TIME_BASED_ON_DATE = '';

const ReservationForm =  ({ onClickReturn }) => {

    const [isTimeClicked, setTimeClicked] = useState(false);
    const [isFoundTime, setFoundTime] = useState(null);

    const selectedStyle = { color: 'red' };
    const unselectedStyle = { color: 'black' };
  
    const handleClickTime = async (selectedDate) =>  {
      // try {
      //   const data = await axios.get(API_FOR_GETTING_AVAILABLE_TIME_BASED_ON_DATE);
      // } catch (e) {
			// 	throw new Error(e);
			// }
    setFoundTime(true);
  };

  return (
    <div className='Reservation-form'>
        <h2>Reservation at Party Color</h2>
        <div className='Reservation-details'>
            <ol className='d-flex'>
                <li style={ !isTimeClicked ? selectedStyle : unselectedStyle }>Select a Date</li>
                <li style={ isTimeClicked ? selectedStyle : unselectedStyle }>Additional details</li>
            </ol>
            <hr />
            <Dates onClickTime={handleClickTime} />
            { isFoundTime ? <TimeList data={times} /> : null }
        </div>
        <button onClick={onClickReturn} className='return-button'>Return</button>
    </div>   
  );
};

const TimeList = ({ data }) => {
  return (
    <div className='TimeList'>
      { times.map(time =>  <Time key={data.id}/>) }
    </div>
  );
};

const Time = ({ time }) => {

  return (
    <div className='Time'>
      <button>7:00PM</button>
    </div>
  );
  
};

const Dates = ({ onClickTime }) => {
  const [number, setNumber] = useState(1);
  const [date, setDate] = useState(convertDateFormat(new Date()));

  const handleClickNumber = (event) => {
    setNumber(event.target.value);
  };
  const handleClickDate = (event) => {
    setDate(convertDateFormat(new Date(event.target.value)));
  };

  function convertDateFormat(current_date) {
    let year = current_date.getFullYear();
    let month = current_date.getMonth() + 1;
    if (month < 10) month = '0' + month.toString();
    let date = current_date.getDate();
    let compatibleDateFormat = year + '-' + month + '-' + date;
    return compatibleDateFormat;
  };

  const handleClickFind = () => {
    onClickTime(date);
  }

  return (
    <div className='Dates'>
        <input type='number' min='1' value={number} onChange={handleClickNumber}/>
        <input type='date' value={date} onChange={handleClickDate}/>
        <button className='find-table-button' onClick={handleClickFind}>Find a table</button>
    </div>
  );
};

const Details = () => {
  return (
    <div className='Details'></div>
  );
};

export default Reservation;