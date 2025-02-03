// src/App.js
import React from 'react';
import { HashRouter as Router, Route, Routes } from 'react-router-dom';
import RecordsetList from './components/RecordsetList';
import RecordsetCreate from './components/RecordsetCreate';
import RecordsetEdit from './components/RecordsetEdit';
import RecordsetDelete from './components/RecordsetDelete';
import RecordsetFields from './components/RecordsetFields';
import './main.css';

function App() {
  return (
    <Router>
      <div className="wpf-app">
        <header className="app-header">
          <nav className="wpf-app-nav">
            <ul className="wpf-app-nav__list">
              <li className="wpf-app-nav__list-item"><a className="wpf-app-nav__link" href="#/">Home</a></li>
              <li className="wpf-app-nav__list-item"><a className="wpf-app-nav__link" href="#/recordsets">Recordsets</a></li>
              <li className="wpf-app-nav__list-item"><a className="wpf-app-nav__link" href="#/recordsets/create">+ Create Recordset</a></li>
            </ul>
          </nav>
        </header>
        <main>
          <Routes>
            {/* Define routes for different screens */}
            <Route path="/" element={<h2>Welcome to the App!</h2>} />
            <Route path="/recordsets" element={<RecordsetList />} />
            <Route path="/recordsets/create" element={<RecordsetCreate />} />
            <Route path="/recordsets/edit/:id" element={<RecordsetEdit />} />
            <Route path="/recordsets/delete/:id" element={<RecordsetDelete />} />
            <Route path="/recordsets/edit/:id/fields" element={<RecordsetFields />} />
          </Routes>
        </main>
      </div>
    </Router>
  );
}

export default App;
