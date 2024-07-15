# -*- coding: utf-8 -*-
##############################################################################
#                                                                            #
#    Copyright (C) Monoyer Fabian (info@olabs.be)                            #
#                                                                            #
# Part of Odoo. See LICENSE file for full copyright and licensing details.   #
##############################################################################

{
    'name': "Font Awesome 4.4.0",
    'version': '1.0.0',
    'author': "O'Labs",
    'category': 'Website',
    'website':'http://www.olabs.be',
    'summary': 'Add fonts awesome 4.4.0',
    'description': """

    Font Awesome, new icons in version 4.4.

        """,
    'depends': ['website'],
    'data': [
        'views/themes.xml',
        ],
    'images':['static/description/banner.jpg'],
    'installable': True,
    'license':"LGPL-3",


}
