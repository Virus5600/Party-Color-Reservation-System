import { useState } from 'react';

import axios from 'axios';

// css
import './Reservation.css';

// images
import logo from './img/logo.png';

// page
import App from './App';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPerson, faEnvelope, faPhone, faCalendar, faClock } from '@fortawesome/free-solid-svg-icons';

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
  { id: 1, time: '5:00PM' },
  { id: 2, time: '6:00PM' },
  { id: 3, time: '7:00PM' },
  { id: 4, time: '8:00PM' },
];

const API_FOR_GETTING_AVAILABLE_TIME_BASED_ON_DATE = '';

const ReservationForm =  ({ onClickReturn }) => {

  const [isTimeClicked, setTimeClicked] = useState(false);
  const [number, setNumber] = useState(1);
  const [date, setDate] = useState('');
  const [time, setTime] = useState('');

  // console.log('number:', number);
  // console.log('date:', date);
  // console.log('time:', time);

  const selectedStyle = { color: 'red' };
  const unselectedStyle = { color: 'black' };

  const handleTimeClick = (time) => {
    setTime(time);
    setTimeClicked(true);
  };

  const handleClickFindTable = (number, date) => {
    setNumber(number);
    setDate(date);
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

            { !isTimeClicked ? <Dates onFindTableClick={handleClickFindTable} onTimeClicked={handleTimeClick} /> : <Details number={number} date={date} time={time} /> }
            
        </div>
        <button onClick={onClickReturn} className='return-button'>Return</button>
    </div>   
  );
};



const Details = ({ number, date, time }) => {
  const [fullName, setFullName] = useState('');
  const [email, setEmail] = useState('');
  const [phoneNumber, setPhoneNumber] = useState('');
  const [isCheckedUpdate, setCheckedUpdate] = useState(false);
  const [specialRequest, setSpecialRequest] = useState('');

  // console.log('full name:', fullName);
  // console.log('email:', email);
  // console.log('phone number:', phoneNumber);
  // console.log('check update:', isCheckedUpdate);
  // console.log('special request:', specialRequest);


  // eto ung url na para sa api (sending to backend the reservation details)
  const API_TO_SEND_RESERVATION = '';


  const handleClickServeButton = async () => {
    const result = await axios.post(API_TO_SEND_RESERVATION, {
      // eto ung json na ipapasa ko satch!!!!!!!!!
      fullname: fullName, // string
      email: email, //string
      phoneNumber: phoneNumber, // string
      specialRequest: specialRequest, // string
      isCheckedUpdate: isCheckedUpdate, //boolean
    }).then(response => {
      console.log('success to send data');
    });
  };

  return (
    <>
      <div className='Details'>
        <DetailsLeft 
          onChangeFullName={setFullName}
          onChangeEmail={setEmail}
          onChangePhoneNumber={setPhoneNumber}
          onCheckUpdate={setCheckedUpdate}
        />
        <DetailsRight number={number} date={date} time={time} onChangeSpecialRequest={setSpecialRequest}/>
      </div>
      <button className='Details-reserve-button'>RESERVE</button>
    </>
  );
};

const DetailsRight = ({ number, date, time, onChangeSpecialRequest }) => {
  const handleChangeSpecialRequest = (event) => {
    onChangeSpecialRequest(event.target.value);
  };

  function addTwoHours(time) {
    const selectedTime = Number(time[0]);
    return (selectedTime + 2) + time.substring(1);
  };

  return (
    <div className='Details-right'>
        <h2>Party Color</h2>
        <div className='Details-right-item'>
          <FontAwesomeIcon className='Details-right-icon' icon={faCalendar} />
          <span>{date}</span>
        </div>
        <div className='Details-right-item'>
          <FontAwesomeIcon className='Details-right-icon' icon={faClock} />
          <span>{time} - {addTwoHours(time)}</span>
        </div>
        <div className='Details-right-item'>
          <FontAwesomeIcon className='Details-right-icon' icon={faPerson} />
          <span>{number} people</span>
        </div>
          <hr />
          <input type='text' placeholder='Add special request(optional)' onChange={handleChangeSpecialRequest} />

      </div>
  );
};

const DetailsLeft = ({
  onChangeFullName,
  onChangeEmail,
  onChangePhoneNumber,
  onCheckUpdate
}) => {
  const handleChangeFullName = (event) => {
    onChangeFullName(event.target.value);
  };
  const handleChangeEmail = (event) => {
    onChangeEmail(event.target.value);
  };
  const handleChangePhoneNumber = (event) => {
    onChangePhoneNumber(event.target.value);
  };
  const handleCheckUpdate = () => {
    onCheckUpdate(true);
  };
  return (
    <div className='Details-left'>
        <div className='Details-left-input '>
          <FontAwesomeIcon className='Details-left-icon adjustment' icon={faPerson} />
          <input type='text' placeholder='Full name' onChange={handleChangeFullName}/><br />
        </div>

        <div className='Details-left-input'>
          <FontAwesomeIcon className='Details-left-icon' icon={faEnvelope} />
          <input type='email' placeholder='Email' onChange={handleChangeEmail}/><br />
        </div>
        <div className='Details-left-input'>
          <FontAwesomeIcon className='Details-left-icon' icon={faPhone} />
          <input type='text' placeholder='Phone number' onChange={handleChangePhoneNumber} /><br />
        </div>
        <div className='Details-left-input'>
          <input type="checkbox" id="update" name="update" onClick={handleCheckUpdate}/>
          <label htmlFor="update">Yes, I want to get email and text updates about my reservation</label><br />
        </div>
      </div>
  );
};



const Dates = ({ onFindTableClick, onTimeClicked }) => {
  const [number, setNumber] = useState(1);
  const [date, setDate] = useState(convertDateFormat(new Date()));
  const [isTimeAvailable, setTimeAvailable] = useState(false);

  const handleClickNumber = (event) => {
    setNumber(event.target.value);
  };
  const handleClickDate = (event) => {
    setDate(convertDateFormat(new Date(event.target.value)));
  };

  const handleClickFindTable = async (selectedDate) =>  {
    // try {
    //   const data = await axios.get(API_FOR_GETTING_AVAILABLE_TIME_BASED_ON_DATE);
    // } catch (e) {
    // 	throw new Error(e);
    // }
    onFindTableClick(number, date);
    setTimeAvailable(true);
  };

  function convertDateFormat(current_date) {
    let year = current_date.getFullYear();
    let month = current_date.getMonth() + 1;
    if (month < 10) month = '0' + month.toString();
    let date = current_date.getDate();
    let compatibleDateFormat = year + '-' + month + '-' + date;
    return compatibleDateFormat;
  };


  return (
    <div className='Dates'>
        <input type='number' min='1' value={number} onChange={handleClickNumber}/>
        <input type='date' value={date} onChange={handleClickDate}/>
        <button className='find-table-button' onClick={handleClickFindTable}>Find a table</button>

        { isTimeAvailable ? <TimeList times={times} onTimeClicked={onTimeClicked}/> : null }
        
    </div>
  );
};

const TimeList = ({ times, onTimeClicked }) => {
  return (
    <div className='TimeList'>
      { times.map(time =>  <Time key={time.id} time={time.time} onTimeClicked={onTimeClicked}/>) }
    </div>
  );
};

const Time = ({ time, onTimeClicked }) => {
  const handleClickTime = () => {
    onTimeClicked(time);
  };

  return (
    <div className='Time'>
      <button onClick={handleClickTime}>{time}</button>
    </div>
  );
  
};



export default Reservation;