<?php

/*
 * Configuration file of Study Methodical document's
 * define constants as follows, for example
 *
 * BKM_DISCIPLE - name of constant (name of bookmark for application)
 * Дисциплина1 - value of constant (name of bookmark in document)
 *
 * define('BKM_DISCIPLE', 'Дисциплина1');
 *
 */

# name of file template
define('SRC_PRGRM', '/Document/PROGRAM.dot');

# bookmarks of PROGRAM document

# some name of disciple
define('PR_DISNAME1', 'disname1');
define('PR_DISNAME2', 'disname2');
define('PR_DISNAME3', 'disname3');

# goal of the discipline
define('PR_DESCR_ST', 'description_study');

# table of disciple and competence study of which
#   is preceded by the study of this discipline
define('PR_TBLDC_BEFORE', 'table_dis_comp_before');

# table of disciple and competence study of which
#   is based by the study of this discipline
define('PR_TBLDC_AFTER', 'table_dis_comp_after');

# table of disciple study of which
#   that generates the same competence
define('PR_TBLDC_EQ', 'table_dis_comp_equal');

# first category of competence
define('PR_FRST_COMP_CAT', 'comp_first_category');

# table of first category competence
define('PR_FRST_COMP_TBL', 'table_comp_group');

# other categories of competences with tables
define('PR_OTH_COMP','comp_next_category');

# knowledges of students
define('PR_KNWLDG_ST', 'knowledge_st');

# ability of students
define('PR_ABLT_ST', 'ability_st');

# skills of students
define('PR_SKL', 'skill_st');

# count of study credits
define('PR_CNT_CREDIT', 'credits_count');

# total count of study hours (without examine hours)
define('PR_CNT_HOURS', 'hours_count');

# structure of study disciple (contain partition of lection)
#   and count hours, point in term
define('PR_LST_PART', 'lection_partition');

# technologies of studies
define('PR_LST_TECH', 'list_technology');

# list of practice works
define('PR_LST_PRACT', 'list_practice');

# list of homework tasks
define('PR_LST_HW', 'list_homework');

# list of questions for examine
define('PR_LST_EXM', 'list_examine');

# list of questions for interm validation
define('PR_LST_VLD', 'list_validation');

# list of independence work students
define('PR_LST_INDEP', 'list_independence');

# list of main literature
define('PR_LST_MAINLTR', 'main_literature');

# list of addidition literature'
define('PR_LST_ADDLTR', 'add_literature');

#end of bookmarks