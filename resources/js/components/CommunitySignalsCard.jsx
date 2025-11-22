import React from 'react';
import { MessageSquare, ThumbsUp, ThumbsDown, Minus, ExternalLink, FileText } from 'lucide-react';

export default function CommunitySignalsCard({ signals }) {
    if (!signals) {
        return (
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center">
                <div className="inline-flex items-center justify-center w-12 h-12 bg-slate-100 rounded-lg mb-4">
                    <FileText className="w-6 h-6 text-slate-400" />
                </div>
                <h2 className="text-lg font-bold text-slate-900 mb-2">Community Signals</h2>
                <p className="text-slate-500">
                    Community analysis data is being processed. Check back soon for insights from social platforms, forums, and review sites.
                </p>
            </div>
        );
    }

    const { platform_breakdown, sentiment_analysis, key_discussions, common_pain_points, feature_requests } = signals;

    return (
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div className="flex items-center mb-6">
                <div className="bg-indigo-100 p-2 rounded-lg mr-3">
                    <MessageSquare className="w-5 h-5 text-indigo-600" />
                </div>
                <h2 className="text-xl font-bold text-slate-900">Community Signals</h2>
            </div>

            <div className="grid md:grid-cols-2 gap-8">
                {/* Left Column: Breakdown & Sentiment */}
                <div className="space-y-8">
                    {/* Platform Breakdown */}
                    <div>
                        <h3 className="text-sm font-semibold text-slate-500 mb-4">Platform Breakdown</h3>
                        <div className="space-y-4">
                            {platform_breakdown?.map((platform, index) => (
                                <div key={index}>
                                    <div className="flex justify-between text-sm mb-1">
                                        <span className="font-medium text-slate-700">{platform.platform}</span>
                                        <span className="text-slate-500">{platform.discussion_volume} Vol</span>
                                    </div>
                                    <div className="w-full bg-slate-100 rounded-full h-2">
                                        <div 
                                            className="bg-indigo-500 h-2 rounded-full" 
                                            style={{ width: `${platform.relevance}%` }}
                                        ></div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Sentiment Analysis */}
                    <div>
                        <h3 className="text-sm font-semibold text-slate-500 mb-4">Sentiment Analysis</h3>
                        <div className="bg-slate-50 rounded-lg p-4 border border-slate-100">
                            <div className="flex items-center justify-between mb-4">
                                <div className="flex items-center text-green-600">
                                    <ThumbsUp className="w-4 h-4 mr-1" />
                                    <span className="font-bold">{sentiment_analysis?.positive}%</span>
                                </div>
                                <div className="flex items-center text-slate-500">
                                    <Minus className="w-4 h-4 mr-1" />
                                    <span className="font-bold">{sentiment_analysis?.neutral}%</span>
                                </div>
                                <div className="flex items-center text-red-500">
                                    <ThumbsDown className="w-4 h-4 mr-1" />
                                    <span className="font-bold">{sentiment_analysis?.negative}%</span>
                                </div>
                            </div>
                            <p className="text-sm text-slate-600 italic">
                                "{sentiment_analysis?.summary}"
                            </p>
                        </div>
                    </div>
                </div>

                {/* Right Column: Discussions & Insights */}
                <div className="space-y-8">
                    {/* Key Discussions */}
                    <div>
                        <h3 className="text-sm font-semibold text-slate-500 mb-4">Key Discussions</h3>
                        <div className="space-y-3">
                            {key_discussions?.map((discussion, index) => (
                                <a 
                                    key={index} 
                                    href={discussion.url} 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    className="block p-3 rounded-lg border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50 transition-all group"
                                >
                                    <div className="flex justify-between items-start">
                                        <h4 className="text-sm font-medium text-slate-900 group-hover:text-indigo-700 line-clamp-1">
                                            {discussion.title}
                                        </h4>
                                        <ExternalLink className="w-3 h-3 text-slate-400 flex-shrink-0 mt-1" />
                                    </div>
                                    <div className="text-xs text-slate-500 mt-1">
                                        {discussion.platform} • {discussion.summary}
                                    </div>
                                </a>
                            ))}
                        </div>
                    </div>

                    {/* Pain Points & Requests */}
                    <div className="grid grid-cols-1 gap-4">
                        <div>
                            <h3 className="text-sm font-semibold text-slate-500 mb-2">Common Pain Points</h3>
                            <ul className="list-disc list-inside text-sm text-slate-600 space-y-1">
                                {common_pain_points?.map((point, index) => (
                                    <li key={index}>{point}</li>
                                ))}
                            </ul>
                        </div>
                        <div>
                            <h3 className="text-sm font-semibold text-slate-500 mb-2">Feature Requests</h3>
                            <ul className="list-disc list-inside text-sm text-slate-600 space-y-1">
                                {feature_requests?.map((req, index) => (
                                    <li key={index}>{req}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
