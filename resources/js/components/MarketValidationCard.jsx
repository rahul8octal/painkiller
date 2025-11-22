import React from 'react';
import { TrendingUp, MessageCircle, ExternalLink, BarChart2 } from 'lucide-react';

export default function MarketValidationCard({ validation }) {
    if (!validation) return null;

    return (
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 className="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                <BarChart2 className="w-5 h-5 mr-2 text-indigo-600" />
                Market Validation
            </h3>

            <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <p className="text-sm text-slate-500 mb-1">Community Interest</p>
                    <div className="flex items-baseline">
                        <span className="text-2xl font-bold text-slate-900">{validation.community_index}</span>
                        <span className="ml-1 text-sm text-slate-500">/100</span>
                    </div>
                </div>
                <div className="bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <p className="text-sm text-slate-500 mb-1">Search Trend</p>
                    <div className="flex items-center">
                        <span className="text-2xl font-bold text-slate-900 capitalize">
                            {validation.keyword_trends?.trend_direction || 'Stable'}
                        </span>
                        {validation.keyword_trends?.trend_direction === 'up' && (
                            <TrendingUp className="w-4 h-4 ml-2 text-green-500" />
                        )}
                    </div>
                </div>
            </div>

            <div className="space-y-4">
                <div>
                    <h4 className="text-sm font-medium text-slate-700 mb-2">Top Keywords</h4>
                    <div className="flex flex-wrap gap-2">
                        {validation.keyword_trends?.top_keywords?.map((kw, index) => (
                            <span key={index} className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {kw.keyword} ({kw.volume}/mo)
                            </span>
                        ))}
                    </div>
                </div>

                <div>
                    <h4 className="text-sm font-medium text-slate-700 mb-2">Validation Signals</h4>
                    <ul className="space-y-2">
                        {validation.validation_links?.slice(0, 3).map((link, index) => (
                            <li key={index} className="text-sm">
                                <a href={link.url} target="_blank" rel="noopener noreferrer" className="flex items-start text-slate-600 hover:text-indigo-600 group">
                                    <ExternalLink className="w-4 h-4 mr-2 mt-0.5 flex-shrink-0 text-slate-400 group-hover:text-indigo-500" />
                                    <span className="line-clamp-1">{link.title}</span>
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </div>
    );
}
