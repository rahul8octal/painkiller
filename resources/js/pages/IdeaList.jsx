import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { TrendingUp, AlertCircle, CheckCircle, ArrowRight } from 'lucide-react';

export default function IdeaList() {
    const [ideas, setIdeas] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get('/api/admin/ideas')
            .then(response => {
                setIdeas(response.data.data);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching ideas:', error);
                setLoading(false);
            });
    }, []);

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold text-slate-900">Startup Ideas Database</h1>
                    <p className="mt-2 text-slate-600">Validated problems with high revenue potential.</p>
                </div>
                <div className="flex space-x-2">
                    <select className="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option>All Categories</option>
                        <option>SaaS</option>
                        <option>Marketplace</option>
                        <option>Mobile App</option>
                    </select>
                    <select className="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option>Sort by Score</option>
                        <option>Sort by Date</option>
                    </select>
                </div>
            </div>

           

            <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {ideas.map((idea) => (
                    <div key={idea.id} className="bg-white rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow duration-200 overflow-hidden flex flex-col">
                        <div className="p-6 flex-1">
                            <div className="flex justify-between items-start mb-4">
                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    idea.problem?.total_score >= 80 ? 'bg-green-100 text-green-800' :
                                    idea.problem?.total_score >= 60 ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-slate-100 text-slate-800'
                                }`}>
                                    
                                    Score: {Math.round(idea.problem?.total_score)}
                                </span>
                                <span className="text-xs text-slate-500">
                                    {new Date(idea.created_at).toLocaleDateString()}
                                </span>
                            </div>
                            
                            <h3 className="text-xl font-semibold text-slate-900 mb-2 line-clamp-2">
                                {idea.problem?.title || 'Untitled Idea'}
                            </h3>
                            
                            <p className="text-slate-600 text-sm line-clamp-3 mb-4">
                                {idea.problem?.body}
                            </p>

                            <div className="flex flex-wrap gap-2 mb-4">
                                {idea.problem?.tags?.slice(0, 3).map((tag, index) => (
                                    <span key={index} className="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200">
                                        {tag}
                                    </span>
                                ))}
                            </div>
                        </div>

                        <div className="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-between items-center">
                            <div className="flex space-x-4 text-sm text-slate-500">
                                <div className="flex items-center" title="Market Validation">
                                    <TrendingUp className="w-4 h-4 mr-1" />
                                    {idea.market_validation?.community_index || 0}%
                                </div>
                                <div className="flex items-center" title="Pain Level">
                                    <AlertCircle className="w-4 h-4 mr-1" />
                                    {idea.problem?.total_score || 0}/100
                                </div>
                            </div>
                            <Link to={`/ideas/${idea.id}`} className="text-indigo-600 hover:text-indigo-700 font-medium text-sm inline-flex items-center">
                                View Report <ArrowRight className="w-4 h-4 ml-1" />
                            </Link>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
