import React from 'react';

import './style.css';

import ReactHtmlParser from 'react-html-parser';

import { useLoaderData } from 'react-router-dom';

export function loader({ params }) {

    if (sessionStorage.getItem('latestannouncement') == null) {
        const announcements = JSON.parse(sessionStorage.getItem('announcement'));
        const _announcements = announcements.filter(announcement => announcement.id == params.announcementId);
        return _announcements[0];
    }

    const announcements = JSON.parse(sessionStorage.getItem('latestannouncement'));
    const _announcements = announcements.filter(announcement => announcement.id == params.announcementId);
    return _announcements[0];

}

export default function AnnouncementContent() {
    const announcement = useLoaderData();
    // console.log(announcement);

    return (


        <div className='AnnouncementContent'>
            <div>
                <div className='AnnouncementContent-title'>
                    {announcement.title}
                </div>
                <div className='AnnouncementContent-summary'>
                    {announcement.summary}
                </div><br />
                {ReactHtmlParser(announcement.content)}
            </div>

        </div>


    );
}
