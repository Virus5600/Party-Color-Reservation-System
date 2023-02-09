import React from 'react';

import './style.css';

const AnnouncementContent = ({ title, summary, children }) => {
    return (


        <div className='AnnouncementContent'>
            <div>
                <div className='AnnouncementContent-title'>
                    {title}
                </div>
                <div className='AnnouncementContent-summary'>
                    {summary}
                </div><br />
                {children}
            </div>

        </div>


    );
};

export default AnnouncementContent;