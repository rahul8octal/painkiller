import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import { ArrowLeft, Target, Zap, Clock, BarChart, AlertTriangle } from 'lucide-react';
import RevenueCard from '../components/RevenueCard';
import MarketValidationCard from '../components/MarketValidationCard';
import CreativeAssets from '../components/CreativeAssets';
import KeywordTrafficCard from '../components/KeywordTrafficCard';

export default function IdeaDetail() {
    const { id } = useParams();
    const [idea, setIdea] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get(`/api/admin/ideas/${id}`)
            .then(response => {
                setIdea(response.data);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching idea:', error);
                setLoading(false);
            });
    }, [id]);

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>
        );
    }

    if (!idea) {
        return (
            <div className="text-center py-10">
                <h2 className="text-2xl font-bold text-slate-900">Idea not found</h2>
                <Link to="/" className="text-indigo-600 hover:text-indigo-500 mt-4 inline-block">
                    Back to Database
                </Link>
            </div>
        );
    }

    const scores = idea.problem.scores || {};

    return (
        <div className="space-y-8">
            <Link to="/" className="inline-flex items-center text-sm text-slate-500 hover:text-slate-700">
                <ArrowLeft className="w-4 h-4 mr-1" /> Back to Database
            </Link>

            {/* Header Section */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
                <div className="flex flex-col md:flex-row justify-between items-start gap-6">
                    <div className="flex-1">
                        <div className="flex items-center gap-3 mb-4">
                            <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-bold ${
                                idea.total_score >= 80 ? 'bg-green-100 text-green-800' :
                                idea.total_score >= 60 ? 'bg-yellow-100 text-yellow-800' :
                                'bg-slate-100 text-slate-800'
                            }`}>
                                Score: {Math.round(idea.problem?.total_score)}
                            </span>
                            <span className="text-sm text-slate-500">
                                Generated {new Date(idea.created_at).toLocaleDateString()}
                            </span>
                        </div>
                        <h1 className="text-3xl font-bold text-slate-900 mb-4">
                            {idea.problem?.title || 'Untitled Idea'}
                        </h1>
                        <p className="text-lg text-slate-600 leading-relaxed">
                            {idea.problem?.body}
                        </p>
                        <div className="mt-6 flex flex-wrap gap-2">
                            {idea.problem?.tags?.map((tag, index) => (
                                <span key={index} className="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-slate-100 text-slate-700">
                                    {tag}
                                </span>
                            ))}
                        </div>
                    </div>
                    
                    {/* Score Breakdown */}
                    <div className="bg-slate-50 rounded-lg p-6 min-w-[300px]">
                        <h3 className="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Business Fit Score</h3>
                        <div className="space-y-3">
                            <ScoreRow label="Opportunity" value={scores.opportunity} icon={Target} />
                            <ScoreRow label="Pain Level" value={scores.pain} icon={AlertTriangle} />
                            <ScoreRow label="Feasibility" value={scores.feasibility} icon={Zap} />
                            <ScoreRow label="Why Now" value={scores.why_now} icon={Clock} />
                            <ScoreRow label="Revenue Potential" value={scores.revenue_potential} icon={BarChart} />
                        </div>
                    </div>
                </div>
            </div>

            {/* Data Grid */}
            <div className="grid md:grid-cols-2 gap-8">
                <RevenueCard revenue={idea.revenue_potential} />
                <MarketValidationCard validation={idea.market_validation} />
            </div>

            {/* Keyword Traffic */}
            <KeywordTrafficCard traffic={idea.market_validation?.keyword_traffic} />

            {/* Creative Assets */}
            <div>
                <h2 className="text-2xl font-bold text-slate-900 mb-6">Execution & Creative Kit</h2>
                <CreativeAssets assets={idea.creative_assets} />
            </div>
        </div>
    );
}

function ScoreRow({ label, value, icon: Icon }) {
    const getColor = (val) => {
        if (val >= 80) return 'text-green-600';
        if (val >= 60) return 'text-yellow-600';
        return 'text-slate-600';
    };

    return (
        <div className="flex justify-between items-center">
            <div className="flex items-center text-slate-600 text-sm">
                <Icon className="w-4 h-4 mr-2 text-slate-400" />
                {label}
            </div>
            <span className={`font-bold ${getColor(value)}`}>{value || 0}/100</span>
        </div>
    );
}
