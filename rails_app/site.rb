class Site < ActiveRecord::Base
  attr_accessible :domain, :onlineStatus
  validates_format_of :domain, :with => /^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/ix

	def to_param
	  slug_param = domain.clone
	  if slug_param.split('.').length < 3 and slug_param.include? '.com'
		slug_param = slug_param.gsub('.com','')
	  	slug_param = domainNoTLD()
	  end
	  slug_param.gsub('.','_').to_s
	end

	def permalink_to
		"/is-"+to_param()+'-down-now'
	end

	def permalink_to_check
		"/check-"+to_param()+'-now'
	end

	def domainNoTLD
		domainAsArray = domain.split('.')
		domainAsArray[0]
	end

	def recordLoadTime
		begin

			#get data
			loadtime = Siteloadtime.generate(domain)
			
			#TODO: refactor this into its own generator method
			#update activerecord
			if self.onlineStatus == 'online' && loadtime > 3 or self.onlineStatus == 'offline' && loadtime < 3 
				statusChanged = true
				self.lastStatusChange = Time.now
			end

			#generate other params
			self.onlineStatus = loadtime < 4 ? 'online' : 'offline'
			self.lastLoadTime = loadtime

			#commit to db
			save
			
			#TODO: refactor this into an activerecord event.
			#tweet
			if Rails.env.production?
				#send tweet if site is down
				if loadtime > 6 and id < 5000 # id is a proxy for popularity
					update = ""+domain+" is having some issues. It's @slowordown http://slowordown.com"+permalink_to()
					begin
						Twitter.update(update)
					rescue Exception => e
					   logger.error "twitter error"

					end

				end
			end
		rescue Exception => e
		   logger.error "#{ e.message } - (#{ e.class })" << "\n" << (e.backtrace or []).join("\n")

		end
	end

end
