// src/components/RecordsetEdit.js
import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';

function RecordsetEdit() {
  const { id } = useParams(); // Get the recordset ID from the URL
  const navigate = useNavigate();

  // State to hold recordset data and loading/error states
  const [recordset, setRecordset] = useState(null);
  const [position, setPosition] = useState('');
  const [error, setError] = useState(null);
  const [message, setMessage] = useState(null);

  useEffect(() => {
    // Fetch the recordset data
    fetch(`http://cjm.local/wp-json/wp-fields/v1/recordsets/${id}`)
      .then((response) => response.json())
      .then((data) => {
        if (data) {
          setRecordset(data);
          setPosition(data.position);
        }
      })
      .catch((err) => setError('Failed to load recordset'));
  }, [id]);

  const handleSubmit = (e) => {
    e.preventDefault();

    // POST request to update the position
    fetch(`http://cjm.local/wp-json/wp-fields/v1/recordsets/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ position }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.message === 'Recordset updated successfully') {
          setMessage('Recordset position updated successfully!');
          setError(null);
          setTimeout(() => navigate('/recordsets'), 2000); // Redirect after 2 seconds
        } else {
          setError('Failed to update recordset');
        }
      })
      .catch(() => setError('An error occurred'));
  };

  if (error) {
    return <div style={{ color: 'red' }}>{error}</div>;
  }

  if (!recordset) {
    return <div>Loading recordset...</div>;
  }

  return (
    <div>
      <h2>Edit Recordset: {recordset.id}</h2>
      {message && <div style={{ color: 'green' }}>{message}</div>}

      <form onSubmit={handleSubmit}>
        <div>
          <label htmlFor="postType">Post Type</label>
          <input
            type="text"
            id="postType"
            value={recordset.post_type}
            readOnly
            style={{ backgroundColor: '#f0f0f0' }} // Grayed out post type
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
        <button type="submit">Update Position</button>
      </form>
    </div>
  );
}

export default RecordsetEdit;
