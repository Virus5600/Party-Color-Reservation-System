import React from 'react';
import Calendar from 'react-calendar'; 
import './MyCalendar.css';

const MyCalendar = () => {

  const [date, setDate] = React.useState(new Date())

  return (
    <div className="MyCalendar">
      <h1 className="header">React Calendar</h1>
      <div className="calendar-container">
        <Calendar onChange={setDate} value={date}/>
      </div>
      <div className="text-center">
        Selected date: {date.toDateString()}
      </div>
    </div>
  )
}

export default MyCalendar;