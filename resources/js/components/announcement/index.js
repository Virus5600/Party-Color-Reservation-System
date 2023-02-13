import React, { useState, useEffect } from 'react';
import axios from 'axios';

import './style.css';

const Announcement = ({ onAnnouncementClick }) => {


    const [announcements, setAnnouncements] = useState([]);


    useEffect(() => {
        const API_ENDPOINT = 'api/react/announcements/fetch';
        async function fetchAnnouncements() {
            try {
                const result = await axios.get(API_ENDPOINT)
                    .then(response => {
                        // console.log(response.data.announcements);
                        // console.log(response.data.announcements[0].poster.replace("{id}", response.data.announcements[0].id));
                        setAnnouncements(response.data.announcements);
                    });
            } catch {
                // console.log('failed fetch announcements');
            }
        }
        fetchAnnouncements();
    }, []);


    return (
        <div className='Announcement' id='Announcement'>
            <h1>Announcement</h1>
            <div className='Announcement-list'>
                {
                    announcements.map(announcement =>
                        <AnnouncementItem
                            key={announcement.id}
                            id={announcement.id}
                            poster={announcement.poster}
                            created_at={announcement.created_at}
                            title={announcement.title}
                            summary={announcement.summary}
                            content={announcement.content}
                            onAnnouncementClick={onAnnouncementClick}
                        />
                    )
                }
            </div>
            <p className='Announcement-more'>more details</p>
        </div>
    );
};


const AnnouncementItem = ({ id, poster, created_at, title, summary, content, onAnnouncementClick }) => {

    function changeDateFormat(string_date) {
        const current_date = new Date(string_date);
        let year = current_date.getFullYear();
        let month = current_date.getMonth() + 1;
        if (month < 10) month = '0' + month.toString();
        let date = current_date.getDate();
        if (Number(date) < 10) date = '0' + date;
        let compatibleDateFormat = year + '.' + month + '.' + date;
        return compatibleDateFormat;
    };

    return (
        <div className='AnnouncementItem' onClick={() => onAnnouncementClick(title, summary, content)}>
            <div className='AnnouncementItem-image'>
                <img src={poster.replace("{id}", id)} />
            </div>
            <div className='AnnouncementItem-description'>
                <span className='date'>{changeDateFormat(created_at)}</span><br />
                <span className='title'>{title}</span><br />
                <span className='summary'>{summary}</span>
            </div>
        </div>

    );
};

export default Announcement;