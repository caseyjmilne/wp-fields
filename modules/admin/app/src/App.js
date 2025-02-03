// src/App.js
import React from 'react';
import { HashRouter as Router, Route, Routes } from 'react-router-dom';
import ListRecordsets from './components/ListRecordsets';
import RecordsetCreate from './components/RecordsetCreate';
import RecordsetEdit from './components/RecordsetEdit';
import './main.css';

function App() {
  return (
    <Router>
      <div className="App">
        <header className="App-header">
          <nav>
            <ul>
              <li><a href="#/">Home</a></li>
              <li><a href="#/recordsets">Recordsets</a></li>
              <li><a href="#/recordsets/create">+ Create Recordset</a></li>
            </ul>
          </nav>
        </header>
        <main>
          <Routes>
            {/* Define routes for different screens */}
            <Route path="/" element={<h2>Welcome to the App!</h2>} />
            <Route path="/recordsets" element={<ListRecordsets />} />
            <Route path="/recordsets/create" element={<RecordsetCreate />} />
            <Route path="/recordsets/edit/:id" element={<RecordsetEdit />} /> {/* New Edit Route */}
          </Routes>
        </main>
      </div>
    </Router>
  );
}

export default App;
