<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = array(
    'post_type'      => 'project',
    'posts_per_page' => 6,
    'paged'          => $paged,
);

$query = new WP_Query($args);

get_header(); ?>

<div class="container">
    <h3>Projects</h3>
    <div class="projects">
        <?php 
        if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            ?>
        <div id="post-<?php the_ID(); ?>" class="project">
            <div class='project-img-box'>
                <?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) { ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
                </a>
                <?php } else{ ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="https://www.colemanrg.com/wp-content/uploads/2022/03/rm373batch2-04.jpg" alt="Avatar" style="width:100%">
                    </a>
                <?php } ?>
            </div>
            <div class="project-content-box">
                <h4 class="title"><?php echo get_the_title();?></h4>
                <div class="description"> 
                    <?php the_excerpt(); ?>
                </div>
                <div class="view-detail">
                    <a href="<?php echo get_permalink();?>" target="_blank" class='view-link-article'>  View Details <i class="fa fa-arrow-right"></i> </a>
                </div>
            </div>
        </div>
        <?php
        endwhile;
        endif;
        ?>
    </div>
    <?php
    
// Display pagination
if ($query->max_num_pages > 1) {
    echo '<div class="pagination">';
    echo paginate_links(array(
        'base'      => get_pagenum_link(1) . '%_%',
        'format'    => '/page/%#%',
        'current'   => max(1, $paged),
        'total'     => $query->max_num_pages,
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
    ));
    echo '</div>';
}
?>
<hr>

</div>

<div class="container">
    <h3>Ajax Call for Projects</h3>
    <a href="#" class="get-projects btn">Get Projects</a>
    <hr>

    <?php echo do_shortcode('[kanye_quotes]'); ?>


</div>



<?php
get_footer();
