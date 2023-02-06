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
                        console.log(response.data.announcements);
                        console.log(response.data.announcements[0].poster.replace("{id}", response.data.announcements[0].id));
                        setAnnouncements(response.data.announcements);
                    });
            } catch {
                console.log('failed fetch announcements');
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
        </div>
    );
};

const AnnouncementItem = ({ id, poster, created_at, title, summary, content, onAnnouncementClick }) => {
    return (
        <div className='Announcement-item' onClick={() => onAnnouncementClick(title, summary, content)}>
            <div className='Announcement-image'>
                <img className='img-fluid' src={poster.replace("{id}", id)} />
            </div>
            <div className='Announcement-description'>
                <span>{created_at}</span><br />
                <span>{title}</span><br />
                <span>{summary}</span>
            </div>
        </div>

    );
};

export default Announcement;