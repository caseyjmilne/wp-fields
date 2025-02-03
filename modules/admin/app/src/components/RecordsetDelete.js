import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';

function RecordsetDelete() {
  const { id } = useParams(); // Get the recordset ID from the URL
  const [recordset, setRecordset] = useState(null);
  const [error, setError] = useState(null);
  const [message, setMessage] = useState(null);
  const navigate = useNavigate(); // Hook to navigate programmatically

  // Fetch the recordset details
  useEffect(() => {
    fetch(`http://cjm.local/wp-json/wp-fields/v1/recordsets/${id}`)
      .then((response) => response.json())
      .then((data) => {
        setRecordset(data);
      })
      .catch(() => {
        setError('Failed to fetch recordset');
      });
  }, [id]);

  // Handle the delete action
  const handleDelete = () => {
    fetch(`http://cjm.local/wp-json/wp-fields/v1/recordsets/${id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        // Add authentication if required
        'X-WP-Nonce': window.wpApiSettings.nonce,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.message === 'Recordset deleted successfully') {
          setMessage('Recordset deleted successfully!');
          setTimeout(() => {
            navigate('/recordsets'); // Redirect to the list after successful deletion
          }, 2000);
        } else {
          setError('Failed to delete recordset');
        }
      })
      .catch(() => {
        setError('An error occurred while deleting the recordset');
      });
  };

  return (
    <div>
      <h3>Delete Recordset</h3>
      {message && <div style={{ color: 'green' }}>{message}</div>}
      {error && <div style={{ color: 'red' }}>{error}</div>}
      {recordset ? (
        <div>
          <p>Are you sure you want to delete the following recordset?</p>
          <ul>
            <li>Post Type: {recordset.post_type}</li>
            <li>Position: {recordset.position}</li>
          </ul>
          <button onClick={handleDelete}>Confirm Delete</button>
          <button onClick={() => navigate('/recordsets')}>Cancel</button>
        </div>
      ) : (
        <p>Loading recordset details...</p>
      )}
    </div>
  );
}

export default RecordsetDelete;
