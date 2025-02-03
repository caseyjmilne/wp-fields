// src/components/RecordsetCreate.js
import React, { useState } from 'react';

function RecordsetCreate() {
  // State to hold form data
  const [postType, setPostType] = useState('');
  const [position, setPosition] = useState('');
  const [message, setMessage] = useState(null);
  const [error, setError] = useState(null);

  // Handle form submission
  const handleSubmit = (e) => {
    e.preventDefault();

    // Make sure the form is filled
    if (!postType || !position) {
      setError('Post Type and Position are required');
      return;
    }

    // POST request to create a new recordset
    fetch('http://cjm.local/wp-json/wp-fields/v1/recordsets', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.wpApiSettings.nonce, // Include the nonce for authentication
      },
      credentials: 'same-origin',  // Make sure cookies are sent for the current origin (WP Admin)
      body: JSON.stringify({
        post_type: postType,
        position: position,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.id) {
          setMessage('Recordset created successfully!');
          setPostType('');
          setPosition('');
          setError(null);
        } else {
          setError('Failed to create recordset');
        }
      })
      .catch(() => {
        setError('An error occurred');
      });
  };

  return (
    <div>
      <h2>Create a New Recordset</h2>
      {message && <div style={{ color: 'green' }}>{message}</div>}
      {error && <div style={{ color: 'red' }}>{error}</div>}

      <form onSubmit={handleSubmit}>
        <div>
          <label htmlFor="postType">Post Type</label>
          <input
            type="text"
            id="postType"
            value={postType}
            onChange={(e) => setPostType(e.target.value)}
          />
        </div>
        <div>
          <label htmlFor="position">Position</label>
          <input
            type="number"
            id="position"
            value={position}
            onChange={(e) => setPosition(e.target.value)}
          />
        </div>
        <button type="submit">Create Recordset</button>
      </form>
    </div>
  );
}

export default RecordsetCreate;
